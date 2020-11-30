/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('pincode_beats', {
    pincode_beat_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    beat_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    pincode: {
      type: "VARBINARY(10)",
      allowNull: false
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    updated_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'pincode_beats'
  });
};
