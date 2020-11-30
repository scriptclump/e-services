/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('promotion_date_time_details', {
    prmt_time_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    prmt_det_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    day_name: {
      type: DataTypes.STRING(50),
      allowNull: false
    },
    day_time_from: {
      type: DataTypes.TIME,
      allowNull: false
    },
    day_time_to: {
      type: DataTypes.TIME,
      allowNull: false
    }
  }, {
    tableName: 'promotion_date_time_details'
  });
};
