/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('vw_salesperson_report', {
    created_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    NAME: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    tbv: {
      type: DataTypes.FLOAT,
      allowNull: true
    },
    order_date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    order_cnt: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    calls_cnt: {
      type: DataTypes.BIGINT,
      allowNull: true
    },
    UOB: {
      type: DataTypes.BIGINT,
      allowNull: false,
      defaultValue: '0'
    },
    ABV: {
      type: "DOUBLE(22,2)",
      allowNull: true
    },
    TLC: {
      type: DataTypes.BIGINT,
      allowNull: false,
      defaultValue: '0'
    },
    ULC: {
      type: DataTypes.BIGINT,
      allowNull: false,
      defaultValue: '0'
    },
    ALC: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    Contribution: {
      type: "DOUBLE(22,2)",
      allowNull: true
    }
  }, {
    tableName: 'vw_salesperson_report'
  });
};
