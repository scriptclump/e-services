/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('putaway_list', {
    putaway_id: {
      type: DataTypes.INTEGER(7),
      allowNull: false,
      primaryKey: true
    },
    putaway_source: {
      type: DataTypes.ENUM('GRN','SR'),
      allowNull: true
    },
    source_id: {
      type: DataTypes.STRING(50),
      allowNull: true
    },
    putaway_by: {
      type: DataTypes.INTEGER(7),
      allowNull: true,
      defaultValue: '0'
    },
    putaway_status: {
      type: DataTypes.INTEGER(7),
      allowNull: true
    },
    create_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    updated_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'putaway_list'
  });
};
